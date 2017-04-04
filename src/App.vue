<template lang="pug">
	.market
		h1.uk-h1 Market Place
		ul.uk-list
			AppTile(v-if="appList!=null", v-for="(app, index) in appList", :app="app")
</template>

<script>

	import Axios from 'axios';
	import AppTile from './components/app-tile.vue';

	export default {
		components: {
			AppTile
		},
		data () {
			return {
				endpoint: '/index.php/apps/market/apps',
				appList: null
			}
		},
		mounted: function() {
			this.get();
		},
		methods: {
			get : function () {
				let self = this;
				Axios.get(this.endpoint)
					.then(function (response) {
						self.appList = response.data;
					})
					.catch(function (error) {
						console.log(error);
					});
			}
		}
	}
</script>

<style lang="scss" scoped>

	@import "styles/variables-theme";

	.market {
		margin: 0 auto;
		padding: $global-gutter;
		max-width: $container-max-width;
	}
</style>