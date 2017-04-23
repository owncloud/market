<template lang="pug">
	div
		ul(v-if="!loading && !failed && applications", uk-grid)
			Tile(v-for="application in applications", :application="application", :key="application.id")
</template>

<script>
	import Tile from './Tile.vue';

	export default {
		components: {
			Tile
		},
		mounted: function () {
			this.$store.dispatch('FETCH_APPLICATIONS')
		},
		computed : {
			loading() {
				return this.$store.state.applications.loading
			},
			failed() {
				return this.$store.state.applications.failed
			},
			applications() {
				if (this.loading || this.failed) {
					return []
				} else {
					return this.$store.getters.applications(this.category)
				}
			},
			category() {
				return this.$route.params.category
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
