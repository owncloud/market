<template lang="pug">
	div
		.uk-card.uk-card-default
			.uk-card-body
				a.uk-button.uk-button-small.uk-width-1-1(v-if="changeable", @click="openModalEditKey", href="#", :class="[ apiKey ? 'uk-button-default' : 'uk-button-primary']") {{ apiKey ? 'Edit API Key' : 'Set API Key' }}
				a.uk-button.uk-button-small.uk-button-default.uk-width-1-1(v-else, @click="openModalViewKey") View API Key

		#edit-api-key(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title Marketplace API Key
				.uk-modal-body
					p Your API-Key is needed inside the ownCloud Market App. Copy and paste it to retrieve your purchased products inside your ownCloud instance.
					label.uk-text-meta.uk-display-block.uk-margin-small-bottom Your personal API Key
					input.uk-input.uk-text-center.-monospace(v-model="newApiKey")
				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close(type='button') Cancel
					button.uk-button.uk-button-primary(type='button', @click="setKey") Save

		#view-api-key(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title Marketplace API Key
				.uk-modal-body
					p
						strong Your API-Key is needed here!
						| Copy and paste it from your Marketplace-Account to retrieve your purchased products here.
					label.uk-text-meta.uk-display-block.uk-margin-small-bottom Your personal API Key
					input.uk-input.uk-text-center.-monospace(v-model="apiKey", readonly)
					.uk-alert-danger(uk-alert)
						p Your API-Key resides in the config.php file which can't be changed here!<br>
				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close(type='button') Close

</template>

<script>

	import Axios from 'axios';

	export default {
		data () {
			return {
				newApiKey : null
			}
		},
		methods : {
			openModalEditKey () {
				UIkit.modal('#edit-api-key').toggle();
			},
			openModalViewKey () {
				UIkit.modal('#view-api-key').toggle();
			},
			setKey () {
				this.$store.dispatch('WRITE_APIKEY', this.newApiKey);

			}
		},
		mounted () {
			this.$store.dispatch('FETCH_APIKEY');
		},
		computed : {
			apiKey () {
				return this.newApiKey = this.$store.state.apikey.apiKey;
			},
			changeable () {
				return this.$store.state.apikey.changeable;
			},
		}
	}
</script>

<style lang="css" scoped>
	.-monospace {
		font-family: "Lucida Console", "Lucida Sans Typewriter", "DejaVu Sans Mono", monospace;
	}
</style>