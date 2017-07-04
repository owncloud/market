<template lang="pug">
	div.uk-margin-medium-top(v-if="!licenseKeyExists")
		button.uk-button.uk-button-small.uk-button-primary.uk-width-1-1(@click="openModalStartEnterpriseKey") {{ t('Start Enterprise trial') }}

		#start-enterprise-trial(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title {{ t('Enterprise Trial Version') }}
				.uk-modal-body
					p Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, <strong>Awesome Apps</strong> ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium.


					ul.uk-list.uk-list-divider
						li(v-if="apiKeyExists").uk-text-success
							p.uk-text-large
								span(uk-icon="icon: check; ratio: 1.5").uk-margin-small-right
								span {{ t('API Key set and valid!') }}

						li(v-if="!apiKeyExists").uk-text-danger
							p.uk-text-large
								span(uk-icon="icon: ban; ratio: 1.25")
								span Unfortunately, you don't have set your marketplace API Key in place. Insert instructions

						li(v-if="!licenseKeyExists").uk-text-danger
							p.uk-text-large
								span(uk-icon="icon: ban; ratio: 1.25").uk-margin-small-right
								span {{ t('No license key configured.') }}

				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close.uk-margin-small-right(type='button') Close

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
				return this.$store.state.apikey.key;
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