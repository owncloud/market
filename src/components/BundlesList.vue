<template lang="pug">
	div
		.uk-position-fixed.uk-position-center(v-show="loading", uk-spinner, uk-icon="icon: spinner")
		ul(v-if="!loading && !failed && bundles").uk-margin-remove.uk-padding-remove
			Tile(v-for="bundle in bundles", :bundle="bundle", :key="bundle.id")

		transition(name="fade")
			.uk-card.uk-card-default.uk-card-body.uk-position-center(v-if="!bundles")
				p.uk-text-center {{ t('No Bundles') }}
</template>

<script>
	import Tile from './BundleTile.vue';

	export default {
		components: {
			Tile
		},
		mounted () {
			this.$store.dispatch('FETCH_BUNDLES');
		},
		computed: {
			loading() {
				return this.$store.state.applications.loading
			},
			failed() {
				return this.$store.state.applications.failed
			},
			bundles() {
				if (this.loading || this.failed) {
					return []
				} else {
					return this.$store.getters.bundles
				}
			}
		},
		methods: {
			t (string, interpolation) {
				if (!interpolation) {
					return this.$gettext(string);
				}
				else {
					// %{interplate} with object
					return this.$gettextInterpolate(string, interpolation);
				}
			}
		}
	}
</script>

<style lang="scss" scoped></style>
