# Contributing to Devqaly

Thank you for showing an interest in contributing to Devqaly! All kinds of contributions are valuable to us. In this guide, we will cover how you can quickly onboard and make your first contribution.

## Submitting an issue

Before submitting a new issue, please search the existing [issues](https://github.com/devqaly/devqaly/issues). Maybe an issue already exists and might inform you of workarounds. Otherwise, you can give new information.

While we want to fix all the [issues](https://github.com/devqaly/devqaly/issues), before fixing a bug we need to be able to reproduce and confirm it. Please provide us with a minimal reproduction scenario using a repository or [Gist](https://gist.github.com/). Having a live, reproducible scenario gives us the information without asking questions back & forth with additional questions like:

- 3rd-party libraries being used and their versions
- a use-case that fails

Without said minimal reproduction, we won't be able to investigate all [issues](https://github.com/devqaly/devqaly/issues), and the issue might not be resolved.

You can open a new issue with this [issue form](https://github.com/devqaly/devqaly/issues/new/choose).

## Projects setup and Architecture

### Requirements

- Node.js v18.16.0 
  - To install Node.js v18.16.0 through NVM (Node Version Manager), follow these steps:
    1. Open your terminal.

    2. Install NVM if you haven't already. You can install NVM by following the instructions at [NVM GitHub](https://github.com/nvm-sh/nvm).

    3. Once NVM is installed, run the following command to install Node.js v18.16.0:
       

        ```bash
        nvm install 18.16.0
                 
        node -v # output: v18.16.0
        ```
     5. You can set Node.js v18.16.0 as your default version with the following command:

        ```bash
        nvm alias default 18

        ```


- [Docker](https://www.docker.com/get-started/)

### Setup the project

The project is a monorepo, meaning that it is a collection of multiple packages managed in the same repository.

### Setting up the backend

The backend is currently using PHP's [Laravel](https://laravel.com/) framework with [Sail](https://laravel.com/docs/10.x/sail).


The first step is to clone your `.env.example` in `apps/api` to `.env.`. Most values should be fine right from the start. 

Then you will need to install Composer depencies using a temporary Docker container with the following command:

```sh
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

Now that the dependencies are installed and the container is ready to start, you can start it by running `./vendor/bin/sail up -d`. To turn off the containers you can do `./vendor/bin/sail down`.

Then, you will need to migrate your database with `./vendor/bin/sail artisan migrate` from within `apps/api` folder.

We highly recommend you to setup an alias to your `./vendor/bin/sail` command by adding this to your `.bashrc` file:

```sh
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

You can follow [Laravel's Sail guideline on how to setup an alias](https://laravel.com/docs/10.x/sail#configuring-a-shell-alias) as well if this doesn't work for you.

### Setting up frontend

The frontend is currently using [VueJS](https://vuejs.org/) framework and is located at `apps/webapp`.

Copy the `.env.example` to `.env`

From `apps/webapp` you will need to run `npm install` to install the dependencies from the project. Then, you can run the dev server with `npm run dev`.

Now, your server should be availble at http://localhost:8000.

### Creating your account

For now, Devqaly is invitation only, meaning you will need to create your account manually by running the following command:

```
sail artisan app:create:accounts:for:company
```

This command should prompt you to questions in order to create your account. Now, you should have received an email in our [Mailhog](https://github.com/mailhog/MailHog) server at the address http://localhost:8025/.

The email should have a button for you to continue your registration.

## Missing a Feature?

If a feature is missing, you can directly [_request_ a new feature](https://github.com/devqaly/devqaly/issues/new?assignees=&labels=feature&projects=&template=feature_request.yml&title=%F0%9F%9A%80+Feature%3A+). You also can do the same by choosing "🚀 Feature" when raising a [New Issue](https://github.com/devqaly/devqaly/issues/new/choose) on our GitHub Repository.
If you would like to _implement_ it, an issue with your proposal must be submitted first, to be sure that we can use it. Please consider the guidelines given below.

## Coding guidelines

To ensure consistency throughout the source code, please keep these rules in mind as you are working:

- All features or bug fixes must be tested by one or more specs (unit-tests).
- We use [Eslint default rule guide](https://eslint.org/docs/rules/), with minor changes. An automated formatter is available using prettier.

## Need help? Questions and suggestions

Questions, suggestions, and thoughts are most welcome. Feel free to open a [GitHub Issue](https://github.com/devqaly/devqaly/issues/new/choose). We can also be reached on our [Discord Server](https://discord.gg/acjcRx5u).

## Ways to contribute

- Try the Devqaly platform and give feedback
- Help with open [issues](https://github.com/devqaly/devqaly/issues) or [create your own](https://github.com/devqaly/devqaly/issues/new/choose)
- Share your thoughts and suggestions with us
- Help create tutorials and blog posts
- Request a feature by submitting a proposal
- Report a bug
- **Improve documentation** - fix incomplete or missing [docs](https://docs.devqaly.com/), bad wording, examples or explanations.

## Missing an SDK for your framework of choice?

If you are in need of an SDK for your framework we do not yet have, you can request a new one by [submitting an issue](https://github.com/devqaly/devqaly/issues/new?assignees=&labels=feature&projects=&template=feature_request.yml&title=%F0%9F%9A%80+Feature%3A+).
We will work with you to make sure that we are building the right tool to solve your issues.
