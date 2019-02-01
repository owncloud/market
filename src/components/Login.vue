<template lang="pug">
	div
		button.uk-button.uk-button-primary.uk-margin-small-top.uk-width-1-1(:disabled="apiKeyIsValid", @click="startMarketplaceLogin")
			span(v-if="!apiKeyIsValid") {{ t('Login') }}
			span(v-else) {{ t('Loggen in') }}
</template>
<script>
	import Mixins from '../mixins';
	import Axios from 'axios';
	import _ from 'underscore';

	export default {
		mixins: [Mixins],
		data () {
			return {
				token: null,
				isLoggedIn: null
			}
		},
		created() {

		},
		mounted() {
			this.$store.dispatch('FETCH_APIKEY', (response) => {
				this.$store.dispatch('WRITE_APIKEY', response.apiKey);
			});
			
			if (!this.$route.query.hasOwnProperty('token')) {
				return
			}

			let token = this.$route.query.token;

			Axios.post(OC.generateUrl('/apps/market/check-marketplace-login-token'), {token: token})
				.then((response) => {
					return this.$store.dispatch('WRITE_APIKEY', response.data.apiKey)
				})
				.catch((error) => {
					console.log(error)
				});

		},
		methods : {
			startMarketplaceLogin () {
				Axios.post(OC.generateUrl('/apps/market/generate-login-challenge'))
					.then((response) => {
						window.location = response.data.loginUrl
					})
					.catch((error) => {
						console.log(error)
					});
			},
		},
		computed : {
			config() {
				return this.$store.getters.config
			},

			apiKeyIsValid () {
				return this.$store.state.apikey.valid;
			},

			apiKeyExists () {
				if (this.$store.state.apikey.key) {
					return this.$store.state.apikey.key.length > 0;
				}

				return false;
			},
		}
	}
</script>

<style lang="css" scoped>
	.-monospace {
		font-family: "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", monospace;
	}

	.intro {
		font-size: 14px;
		line-height: 24px;
	}
</style>
