<template lang="pug">
	.uk-card.uk-card-default.uk-margin-bottom
		.uk-card-header
			h1.uk-h3
				router-link(:to="{ name: 'index' }") {{ t('Market') }}

		.uk-card-body
			ul.uk-nav-default.uk-nav-parent-icon(uk-nav, :v-if="!loading && !failed")
				li
					router-link(:to="{ name: 'index' }") {{ t('Show all') }}
				li
					router-link(:to="{ name: 'Bundles' }") {{ t('App Bundles') }}

				li.uk-nav-header {{ t('Categories') }}

				li(v-for="category in categories")
					router-link(:to="{ name: 'byCategory', params: { category: category.id }}") {{ category.translations.en.name }}

				li(v-if="updateList.length > 0")
					router-link(:to="{ name: 'UpdateList' }") {{ t('Updates') }}
						span.uk-badge.uk-margin-small-left {{ updateList.length }}

				li.uk-nav-header {{ t('Settings') }}

				apiform

</template>

<script>
	import Apiform from './ApiForm.vue'

	export default {
		mounted () {
			this.$store.dispatch('FETCH_CATEGORIES')
		},
		methods: {
			t(string) {
				return this.$gettext(string);
			}
		},
		computed: {
			loading() {
				return this.$store.state.categories.loading
			},
			failed() {
				return this.$store.state.categories.failed
			},
			categories() {
				if (this.loading || this.failed) {
					return []
				} else {
					return this.$store.state.categories.records
				}
			},
			updateList() {
				return this.$store.getters.updateList
			}
		},
		components: {
			Apiform,
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	h1 {
		a {
			text-decoration: none;
		}
	}

	.uk-badge {
		font-size: 0.75rem;
	}
</style>
