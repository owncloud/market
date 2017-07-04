<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				navigation
				trial

			main.uk-width-expand
				router-view
</template>

<script>
	import Navigation from './components/Navigation.vue'
	import Trial from './components/Trial.vue'

	export default {
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS');
		},
		components: {
			Navigation,
			Trial
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