<template lang="pug">
	div
		button.uk-button.uk-button-primary.uk-margin-small-top.uk-width-1-1(@click="openModalStartEnterpriseKey", :disabled="licenseKeyExists") {{ t('Start Enterprise trial') }}

		#start-enterprise-trial(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title {{ t('Enterprise Trial Version') }}
				.uk-modal-body
					p Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, <strong>Awesome Apps</strong> ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.

					.uk-alert-danger(uk-alert, v-if="!apiKeyExists")
						p Unfortunately, you don't have set your marketplace API Key in place.<br>
							a(href="#", @click.prevent="openModalEditKey") Set API key here
							| &nbsp;and try again.

				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close.uk-margin-small-right(type='button') Close
					button.uk-button.uk-button-primary.uk-position-relative.uk-align-right.uk-margin-remove-bottom(:disabled="!apiKeyExists") {{ t('Start trial') }}

</template>

<script>

	import Axios from 'axios';

	export default {
		data () {
			return {
				newKey : null
			}
		},
		methods : {
			openModalStartEnterpriseKey () {
				UIkit.modal('#start-enterprise-trial').toggle();
			},
			openModalEditKey () {
				UIkit.modal('#edit-api-key').toggle();
			},
			t (string, interpolation) {
				if (!interpolation) {
					return this.$gettext(string);
				}
				else {
					// %{interplate} with object
					return this.$gettextInterpolate(string, interpolation);
				}
			}
		},
		mounted () {
			this.$store.dispatch('CHECK_LICENSE_KEY');
		},
		computed : {
			licenseKeyExists () {
				return this.$store.state.licenseKeyExists;
			},

			apiKeyExists () {
				if (this.$store.state.apikey.key)
					return this.$store.state.apikey.key.length > 0;

				return false;

			},

			changeable () {
				return this.$store.state.apikey.changeable;
			},
			loading () {
				return this.$store.state.apikey.loading;
			},
			valid () {
				return this.$store.state.apikey.valid;
			},
		}
	}
</script>

<style lang="css" scoped>
	.-monospace {
		font-family: "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", monospace;
	}
</style>