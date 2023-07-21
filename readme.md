<div align="center">
  <a href="https://devqaly.com" target="_blank">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="./assets/images/logo.svg">
    <img src="./assets/images/logo.svg" width="280" alt="Logo"/>
  </picture>
  </a>
</div>

<h1 align="center">
You record your screen, while Devqaly records important events for easier and faster debugging
</h1>

<div align="center">
The ultimate service allowing your developers or quality assurance engineers to record their screens while Devqaly 
records important information such as network requests, clicks, console logs, database transactions and many more.
</div>

<p align="center">
    <br />
    <a href="https://docs.devqaly.com" rel="dofollow"><strong>Explore the docs »</strong></a>
    <br />
</p>

## ⭐️ Why Devqaly?
Devqaly understands the frustration between developers and quality assurance engineers when in the development phase of 
building a new feature. The QA have found a bug and writes instructions on how to reproduce it but you, as a developer
needs more information.

That's where Devqaly enters: your QA simply clicks a button to start recoding their screen
while Devqaly records:

- **network requests**: check out which network requests have been made from your frontend application
- **database transactions**: see which database transactions have happened because of a network request
- **clicks**: see which elements have been clicked
- **console logs**: see which logs have been written to your frontend console
- **window resize**: understand if there was a resizing of the screen
- **URL changes**: see which URLs have been visited while a session was being recorded

## ✨Features

- 🌈 Easily integrate in any project. [Getting started with Devqaly](https://docs.devqaly.com/getting-started/quickstart/)
- 💅 Easily filter out events you wouldn't like to record
- 🚀 See a video of the screen while the bug was being reproduced
- 🛡 Understand which network request have started a database transaction
- 📦 Understand how many resources a network request have spawned in your infrastructure
- 🛡 Debug easier and faster by seeing network requests being sent from your frontend app (and their responses)
- 👨‍💻 Community driven

## 🏎️ Getting started

Before getting started you should have an account created at https://devqaly.com and a project. Then, you will need
to copy the project's key.

To get started with Devqaly the first step is to install the SDK in your frontend project:

```bash
npm install @devqaly/browser
```

Then, you can initialize the SDK in your project:

```typescript
import { DevqalySDK } from '@devqaly/browser'

const devqalySDK = new DevqalySDK({
  // You can obtain the project key by visiting your project's list at
  // https://app.devqaly.com/projects
  projectKey: '<project-key>',
})

devqalySDK.showRecordingButton()
```

By adding the SDK to your frontend project, you will allow anyone with access to the project to record a session.

The person will be redirected to https://app.devqaly.com (or a self-hosted version) where the developer or QA will be 
able to see the recording with all important events that will help your developers debug faster.

## 🖥️ Connecting your backend

To record `database transactions` and `logs` in your backend, you will need to install our SDK for your backend. 
Currently, we have two SDKs:

- [Get started with PHP](https://docs.devqaly.com/getting-started/backend/php/)
- [Get started with Laravel](https://docs.devqaly.com/getting-started/backend/laravel/)

We are currently working on adding more Devqaly SDKs to make it easier than ever to integrate Devqaly into your
backend project.

## 💻 Need Help?

We are more than happy to help you. If you are getting any errors or facing problems while working on this project, join our [Discord server](https://discord.gg/acjcRx5u) and ask for help. 
We are open to discussing anything related to the project.

## 🛡️ License

Devqaly is licensed under the MIT License - see the [LICENSE](https://github.com/devqaly/devqaly/blob/master/LICENSE) file for details.

## ❤️ Contributors
Devqaly wouldn't be possible without the hard working of all the contributors.
From the bottom of our hearts, thank you very much for all the hard work that each contributor have done.

<img src="https://contributors-img.web.app/image?repo=devqaly/devqaly" />