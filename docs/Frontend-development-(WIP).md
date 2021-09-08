## How to set up your frontend development environment

The front end section is based on the [Vue.js 2.0](https://vuejs.org/) and [UIkit 3.0.x](https://getuikit.com/) _(currently still in beta)_ framework. The build process is based on Webpack. To get started, you need the latest LTS version of [node.js](https://nodejs.org) installed and up to date.

***

### Setup

1. Change to the `/apps/market` folder and install all dependencies and run:

`npm install`

2. If no errors occur, you can get webpack started (watcher included) by running:

`npm run dev`

***

### Js- / Vue-development

The base App and all its components are written in *.vue files. The latter are to be placed in the /src/components folder. API calls are handled using the [Axios HTTP client](https://www.npmjs.com/package/axios) and should remain in the store.js using [VUEX](https://vuex.vuejs.org/en/).

_Make sure to scope component css: `<style lang="[...]" scoped>` to prevent propagation to "unwanted places"_

### Styling

Styles should mainly come from the UIkit library. However some styles can be scoped to the components.
Base overrides are handled in the `src/styles/variables-themes.scss` file.