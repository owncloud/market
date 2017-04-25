<template lang="pug">
	div
		.uk-position-fixed.uk-position-center(v-show="loading", uk-spinner, uk-icon="icon: spinner")
		transition(name="fade")
			.uk-card.uk-card-default(v-if="!loading && !failed && applications")
				.uk-card-header
					h2.uk-h3 {{ t('Updates') }}

				.uk-card-body
					table.uk-table.uk-table-striped
						thead
							tr
								th {{ t('App') }}
								th {{ t('Developer') }}
								th {{ t('Update Info') }}
								th &nbsp;
						tbody
							tr(v-for="application in applications")
								td.uk-text-bold {{ application.name }}
								td {{ application.publisher.name }}
								td
									span {{ application.installInfo.version }}
									span(uk-icon="icon: arrow-right").uk-margin-small-left.uk-margin-small-right
									span {{ application.updateInfo }}
								td
									button.uk-button.uk-button-small.uk-button-primary.uk-align-right.uk-margin-remove.uk-position-relative(@click="update(application.id)", :disabled="updating(application.id)")
										span(v-if="updating(application.id)")
											.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.4", style="left:5px;")
											| &nbsp;&nbsp;&nbsp;&nbsp;updating
										span(v-else)
											| update
							tr(v-if="applications.length === 0")
								td(colspan="4").uk-text-center
									span.uk-text-primary {{ t('All apps are up to date') }}

</template>

<script>
	import Tile from './Tile.vue';

	export default {
		components: {
			Tile
		},
		mounted () {
			this.$store.dispatch('FETCH_APPLICATIONS')
		},
		methods: {
			update (id) {
				this.$store.dispatch('UPDATE_APPLICATION', id)
			},
			updating(id) {
				return _.contains(this.$store.state.updating, id)
			},
			t(string) {
				return this.$gettext(string);
			}
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
					return this.$store.getters.updateList
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	aside {
		width: 260px;
	}

	.uk-button-small {
		line-height: 20px;
		font-size: 0.775rem;
	}
</style>
