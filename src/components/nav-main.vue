<template lang="pug">
	.uk-card.uk-card-default
		.uk-card-header
			h1.uk-h3 Market
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
		data () {
			return { categories : null }
		},
		mounted : function () {
			let self = this;
			Axios.get('/index.php/apps/market/categories')
				.then(function (response) {
					self.categories = response.data;
				})
				.catch(function (error) {
					console.log(error);
				});
		}
	}
</script>

<style lang="scss" scoped>

	@import "../styles/variables-theme";

</style>