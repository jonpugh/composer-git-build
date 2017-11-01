# Composer Plugin: git-build

The goal of this tool is to make it as easy as possible to commit your ignored directories to git.

Otherwise known as "artifact-building", this command will soon:

1. Automatically modify your .gitignore file to remove entries we want to use.
2. Create a branch (if specified).
3. Commit all code that is no longer ignored.
4. Create a tag (if specified).
5. Push branch or tag to origin.

This plugin fulfills a longtime dream of many people, but the code was borrowed heavily from [Acquia's BLT](https://github.com/acquia/blt) command [`deploy` command](https://github.com/acquia/blt/blob/9.1.x/src/Robo/Commands/Deploy/DeployCommand.php).

## Usage

In your composer project:

`composer require jonpugh/composer-git-build`

In your .gitignore:

```
### INCLUDE IN COMPOSER GIT BUILD ### 
# Items below this line will be removed by `git-build` command.
# Code in these folders will be automatically committed.
```
(Not Yet Functional!)

Then there is a new composer command:

```
  git-build        Add all vendor code and ignored dependencies to git.
```

```
Usage:
  git-build [options]

Options:
  -b, --branch=BRANCH            Branch to create.
  -t, --tag=TAG                  Tag to create.
  -m, --commit-msg=COMMIT-MSG    Commit message to use.
      --ignore-dirty             Allow committing even if git working copy is dirty (has modified files).
      --dry-run                  Build and commit to the repository but do not push.
```


## Thanks

- @acquia/blt team for getting the ball rolling.
- @generalredneck for his .gitignore trick  his session from Cornell DrupalCamp: https://www.youtube.com/watch?v=WMd60xmQvlY&feature=youtu.be
