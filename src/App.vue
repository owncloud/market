<template>
	<div class="example">
		<pre>{{ list }}</pre>
	</div>
</template>

<script>

	import Axios from 'axios';

	export default {
		data () {
			return {
				list: 'loading'
			}
		},
		mounted: function() {
			this.get();
		},
		methods: {
			get : function () {

				let self = this;
//				let endpoint = '/demo.json';
				let endpoint = '/index.php/apps/market/apps';

				Axios.get(endpoint)
					.then(function (response) {
						self.list = response.data.list;
					})
					.catch(function (error) {
						console.log(error);
					});
			}
		}
	}
</script>

<style lang="less" scoped>
</style>