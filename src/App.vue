<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation
				apiform

			main.uk-width-expand
				router-view
</template>

<script>
	import Navigation from './components/Navigation.vue'
	import Apiform from './components/ApiForm.vue'

	export default {
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS');
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS');
			this.$store.dispatch('FETCH_BUNDLES');
		},
		components: {
			Navigation,
			Apiform
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