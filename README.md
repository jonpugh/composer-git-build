# Composer Plugin: git-build

The goal of this tool is to make it as easy as possible to commit your ignored directories to git.

Otherwise known as "artifact-building", this command will:

1. If --build-dir is specified, create a new repo and add your git remotes.
2. Pull in latest changes from current branch.
3. Automatically modify your .gitignore file, allowing vendor and other code to be added.
4. Commit all new code that is no longer ignored.
5. Create a new branch or tag.
6. Push the branch or tag to origin.

This plugin code was borrowed heavily from [Acquia's BLT](https://github.com/acquia/blt) command [`deploy` command](https://github.com/acquia/blt/blob/9.1.x/src/Robo/Commands/Deploy/DeployCommand.php).

## Usage

1. Add to your composer project or globally:

    `composer require jonpugh/composer-git-build`
    
     -or-
    
    `composer global require jonpugh/composer-git-build`

2. Add to your .gitignore file:

    ```
    ## IGNORED IN GIT BUILD: ##
    # Items below this line will retained in artifacts built with the `composer git-build` command.
    ```
    
3. Add to your composer.json file:

    ```json
    {
        "config": {
            "git.remotes": [
                "git@github.com:organization/build-repo.git",
                 "svn@acquia.com"
            ]
        }
    }
    ```
    
    The new built repo will have these remotes added automatically, and the tag or branch pushed to them.
    
4. Run `composer git-build`:
    
    ```
    Usage:
       git-build [options]
     
     Options:
       -b, --build-dir[=BUILD-DIR]    Directory to create the git artifact. Defaults to the composer working-dir option.
           --branch=BRANCH            Branch to create.
           --tag=TAG                  Tag to create.
       -m, --commit-msg=COMMIT-MSG    Commit message to use.
           --ignore-dirty             Allow committing even if git working copy is dirty (has modified files).
           --dry-run                  Build and commit to the repository but do not push.
    
    ```


## Thanks

- @acquia/blt team for getting the ball rolling.
- @generalredneck for his .gitignore trick  his session from Cornell DrupalCamp: https://www.youtube.com/watch?v=WMd60xmQvlY&feature=youtu.be
