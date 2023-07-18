---
title: Introduction
description: Understand how and why you would be using Devqaly
---

# Introduction

Devqaly is an open-source session replay, built for engineering teams to help them build rich product 
experiences without constantly reinventing the wheel.

Allow users during your staging and local development to record their screens while Devqaly records important
information to help you, as a developer to debug issues faster.

# Why

As a developer, speaking to quality assurance engineers (QAE) is something we often do while developing applications.
This communication can be hard when QAE are not technical people. Even then, we, as developers lack information that is 
vital to debug the issue at hand.

We at Devqaly understand that this is a problem that needs solving. There should be a better way to have developers and 
quality assurance engineers to collaborate together and ship products faster to clients.

# How it works

Devqaly sets up a button in your frontend application that allows your QAE to record their screen while reproducing a bug,
or some functionality that doesn't quite work as defined in the requirements.

While the QAE is reproducing the bug, Devqaly will record important information that will enable the developer to easily 
debug the issues at hand.

Optionally, you can install our SDK in your backend and Devqaly will allow you to discover resources that a single
network request have used such as:

- **database transactions**: see which database transactions were invoked on a specific request
- **logs**: which logs have been written for a specific request
- **exceptions**: know when your application have thrown an exception either from the frontend or backend (or any microservice)
