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
		mounted: function () {
			this.$store.dispatch('FETCH_APPLICATIONS')
			this.$store.dispatch('FETCH_CATEGORIES')
		},
		components: {
			Navigation
		}
	}
</script>

<style lang="scss" scoped>
	#market-app {
		min-height: 100%;
	}

	aside {
		width: 300px;
	}
</style>