<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation

			main.uk-width-expand
				router-view
</template>

<script>
	import Navigation from './components/Navigation.vue';

	export default {
		components: {
			Navigation
		}
	}
</script>

<style lang="scss" scoped>
	aside {
		width: 300px;
	}
</style>
