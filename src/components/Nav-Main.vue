<template lang="pug">
	.uk-card.uk-card-default
		.uk-card-header
			h1.uk-h3 {{ title }}
		.uk-card-body
			ul.uk-nav-default.uk-nav-parent-icon(uk-nav)
				li.uk-nav-header Categories
				li(v-for="category in categories")
					a(href='#') {{ category.translations.en.name }}

				li.uk-nav-header My Apps
				li
					a(href='#')
						span.uk-margin-small-right(uk-icon='icon: check')
						| Active
				li
					a(href='#')
						span.uk-margin-small-right(uk-icon='icon: ban')
						| Disabled

</template>

<script>
	import Axios from 'axios';

	export default {
		computed : {
			categories() {
				return this.$store.state.categories;
			},
			title() {
				return this.$gettext('Market');
			}
		}
	}
</script>

<style lang="scss" scoped>

	@import "../styles/variables-theme";

</style>
