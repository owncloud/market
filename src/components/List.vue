<template lang="pug">
	div
		ul(v-if="appList", uk-grid)
			AppTile(v-for="(app, index) in appList", :app="app", :key="app.id", :index="index")

		div(uk-spinner, v-if="!appList")
</template>

<script>

	// Components
	import AppTile from './Tile.vue';

	export default {
		components: {
			AppTile
		},
		computed : {
			appList() {
				return this.$store.getters.apps;
			}
		}
	}
</script>

<style lang="scss" scoped>

	@import "../styles/variables-theme";

	aside {
		width: 260px;
	}

	.market {
		padding: $global-gutter;
	}

</style>