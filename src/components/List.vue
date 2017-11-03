<template lang="pug">
	div
		.uk-position-fixed.uk-position-center(v-show="loading", uk-spinner, uk-icon="icon: spinner")
		ul(v-if="!loading && !failed && applications", uk-grid)
			Tile(v-for="application in applications", :application="application", :key="application.id")

		transition(name="fade")
			.uk-card.uk-card-default.uk-card-body.uk-position-center(v-if="applications.length === 0 && !loading && !failed")
				p.uk-text-center {{ t('No apps in %{category}', { category }) }}
</template>

<script>
	import Mixins from '../mixins';
	import Tile from './Tile.vue';

	export default {
		mixins: [Mixins],
		components: {
			Tile
		},
		computed: {
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
