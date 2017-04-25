<template lang="pug">
	.uk-card.uk-card-default
		.uk-card-header
				h1.uk-h3
					router-link(:to="{ name: 'index' }") {{ t.title }}

		.uk-card-body
			ul.uk-nav-default.uk-nav-parent-icon(uk-nav, :v-if="!loading && !failed")
				li.uk-nav-header {{ t.categories }}
				li(v-for="category in categories")
					router-link(:to="{ name: 'byCategory', params: { category: category.id }}") {{ category.translations.en.name }}
				li.uk-nav-divider
				li
					router-link(:to="{ name: 'index' }") {{ t.all }}
				li(v-if="updateList.length > 0")
					router-link(:to="{ name: 'UpdateList' }") Updates
						span.uk-badge.uk-margin-small-left {{ updateList.length }}
</template>

<script>
	export default {
		mounted: function () {
			this.$store.dispatch('FETCH_CATEGORIES')
		},
		computed : {
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
			},
			t() {
				return {
					title: this.$gettext('Market'),
					categories: this.$gettext('Categories'),
					all: this.$gettext('show all'),
				}
			}
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
