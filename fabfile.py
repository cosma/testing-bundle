from __future__ import print_function
from fabric.api import local, run, env, parallel, roles, hide, sudo, hosts, \
    task, execute, cd, warn_only
from fabric.contrib.files import exists
from fabric.contrib.console import confirm
from fabric.colors import red, green, yellow
from datetime import timedelta
import json
import os
import sys

env.use_ssh_config = True
env.remote_interrupt = True

def ensure_module(module_name):
    """ ensure module is installed """
    try:
        __import__(module_name)
    except ImportError:
        python = sys.executable
        print(red("Could not find {module_name}."
                  " Installing it for you now:".format(**locals())))
        local('sudo {python} -m pip install {module_name}'.format(
            **locals()))


def get_current_tag():
    local('git fetch')
    current_tag = local('git tag --sort version:refname | tail -n1',
                        capture=True)
    return current_tag


def get_next_tag():
    current_tag = get_current_tag()
    versions = current_tag.split('.')
    versions[2] = str(int(versions[2]) + 1)
    tag = '.'.join(versions)
    return tag


@task
def tag(tag=None):
    '''create a tagged from master'''
    ensure_module('requests')
    branch = local('git rev-parse --abbrev-ref HEAD', capture=True)
    if 'master' != branch:
        if not confirm(
                'You are not in master branch.'
                ' Do you REALLY want to continue?',
                default=False
        ):
            sys.exit(0)
    if not tag:
        tag = get_next_tag()
    import requests

    if requests.get(
            'https://circleci.com/api/v1/project/cosma/testing-bundle/'
            'tree/{branch}?limit=1&'
            'circle-token=8accb19a030b5c34c84f22616602eea4846472d2'.format(
                **locals()), headers={
                'Accept': 'application/json'}
    ).json()[0]['status'] not in ['fixed', 'success']:
        if not confirm(
                'CircleCI is not green. Do you REALLY want to continue?',
                default=False
        ):
            sys.exit(0)
    if confirm('do you want to create tag {tag}?'.format(
            tag=green(tag)
    )):
        local('git tag {}'.format(tag))
        local('git push --tags')
        local('git push')


@task
def git_cleanup():
    '''cleanup git locally and remote'''
    local('git fetch')
    print("Deleting remote tracking branches whose "
          "tracked branches on server are gone...")
    local('git remote prune origin')
    print("Searching all remote branches except master "
          "that are already merged into master...")
    get_remote_merged_branches = None
    get_remote_merged_branches = local(
        'git branch -r --merged origin/master'
        ' | grep -v master | grep -v master || exit 0',
        capture=True)

    if get_remote_merged_branches:
        print(get_remote_merged_branches)
        if confirm("Do you want to delete those branches on the server?"):
            print("Deleting...")
            local("echo '{}' | sed 's#origin/##' | xargs -I {{}}"
                  " git push origin :{{}}".format(
                      get_remote_merged_branches))
            local('git remote prune origin')
        else:
            print("ok, will not delete anything.")
    else:
        print('No remote merged branches found')
    print("Deleting all local branches (except current)"
          " that are already merged into local master...")
    local("git branch --merged master | grep -v master | grep -v master "
          "| grep -v '\*' | xargs git branch -d")
    print("Checking for unmerged local branches...")
    local('git branch --no-merged master')
