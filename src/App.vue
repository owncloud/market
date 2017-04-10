<template lang="pug">
	.market.uk-grid-large(uk-grid)
		aside
			NavMain
		main.uk-width-expand
			ul(v-if="appList!=null", uk-grid)
				AppTile(v-for="app in appList", :app="app", :key="app.id")

			div(uk-spinner, v-if="!appList")
</template>

<script>

	import Axios from 'axios';

	// Components
	import AppTile from './components/app-tile.vue';
	import NavMain from './components/nav-main.vue';

	export default {
		components: {
			AppTile,
			NavMain
		},
		data () {
			return {
				menu : null,
				endpoint: OC.generateUrl('/apps/market/apps'),
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

	aside {
		width: 260px;
	}

	.market {
		padding: $global-gutter;
	}

</style>
