<template lang="pug">
	div(v-if="!unborn")
		button.uk-button.uk-button-primary.uk-margin-small-top.uk-width-1-1(v-if="!licenseKeyExists && !loading", @click="openModalStartEnterpriseKey") {{ t('Start Enterprise trial') }}

		#start-enterprise-trial(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title {{ t('Enterprise Trial Version') }}
				.uk-modal-body
					div(v-if="!licenseKeyExists")
						p.intro Take your ownCloud to the next level and start your 30 day ownCloud Enterprise Trial today!

						.uk-alert-danger(uk-alert, v-if="!apiKeyExists")
							p An Marketplace API key is required!&nbsp;
								a(href="#", @click.prevent="openModalEditKey") Set API key here
								| &nbsp;and try again.

						.uk-margin.uk-grid-small.uk-child-width-auto(uk-grid)
							label
								input.uk-checkbox(v-model="legalChecked", type="checkbox").uk-margin-small-right
								| I accept the <a href="https://owncloud.com/licenses/owncloud-confidentiality-agreement" target="_blank" class="uk-text-primary">ownCloud enterprise confidentiality agreement</a> and the <a href="https://owncloud.com/licenses/owncloud-commercial" target="_blank" class="uk-text-primary">ownCloud Commercial License</a>.

					.uk-alert-success(v-if="licenseKeyExists", uk-alert)
						p.intro
							strong {{ t('Awesome! Your 30 day trial is ready to go!') }}

				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close.uk-margin-small-right(type='button') Close
					button.uk-button.uk-button-primary.uk-position-relative.uk-align-right.uk-margin-remove-bottom(v-if="loading", disabled)
						.uk-position-small.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")
						| &nbsp;&nbsp;&nbsp;&nbsp; {{ t('Loading') }}
					button.uk-button.uk-button-primary.uk-position-relative.uk-align-right.uk-margin-remove-bottom(v-if="!licenseKeyExists && !loading", @click="requestLicenseKey", :disabled="!legalChecked || !apiKeyExists || loading") {{ t('Start trial') }}

</template>

<script>

	import Mixins from '../mixins';
	import Axios from 'axios';
	import _ from 'underscore';

	export default {
		mixins: [Mixins],
		data () {
			return {
				legalChecked : false
			}
		},
		methods : {
			openModalStartEnterpriseKey () {
				UIkit.modal('#start-enterprise-trial').toggle();
			},
			openModalEditKey () {
				UIkit.modal('#edit-api-key').toggle();
			},
			requestLicenseKey () {
				this.$store.dispatch('REQUEST_LICENSE_KEY')
				.then(this.$router.push({ name: 'Bundles' }))
				.catch(() => {
					console.warn('REQUEST_LICENSE_KEY failed')
				})
			}
		},
		mounted () {
			this.$store.dispatch('CHECK_LICENSE_KEY');
		},
		computed : {
			licenseKeyExists () {
				return this.$store.state.licenseKey.exists;
			},

			apiKeyExists () {
				if (this.$store.state.apikey.key)
					return this.$store.state.apikey.key.length > 0;

				return false;
			},

			loading () {
				return this.$store.state.licenseKey.loading;
			},

			unborn () {
				return this.$store.state.licenseKey.unborn;
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
