<template lang="pug">
	.uk-padding
		.uk-grid-large(uk-grid)
			aside.uk-width-auto
				nav-main
			main.uk-width-expand
				router-view
</template>

<script>
	import Axios from 'axios';

	export default {
		mounted : function() {
			// Initially fetch data
			this.fetchAll();
		},
		methods : {
			fetchAll () {
				this.fetchAppsList();
				this.fetchCategoriesList();
			},
			fetchAppsList () {
				let self = this;

				Axios.get(OC.generateUrl('/apps/market/apps'))
					.then(function (response) {
						self.$store.state.apps = response.data;
					})
					.catch(function (error) {
						console.log(error);
					});
			},
			fetchCategoriesList () {
				let self = this;

				Axios.get(OC.generateUrl('/apps/market/categories'))
					.then(function (response) {
						self.$store.state.categories = response.data;
					})
					.catch(function (error) {
						console.log(error);
					});
			},
		}
	}
</script>

<style scoped>
	aside {
		width: 300px;
	}
</style>
