<template lang="pug">
	div
		.uk-card.uk-card-default
			.uk-card-body
				button.uk-button.uk-button-small.uk-width-1-1(v-if="changeable", @click="openModalEditKey", :disabled="loading" ,:class="[ key ? 'uk-button-default' : 'uk-button-primary']") {{ key ? 'Edit API Key' : 'Set API Key' }}
				button.uk-button.uk-button-small.uk-button-default.uk-width-1-1(v-else, @click="openModalViewKey") View API Key

		#edit-api-key(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title Marketplace API Key
				.uk-modal-body
					p Your API-Key is needed inside the ownCloud Market App. Copy and paste it to retrieve your purchased products inside your ownCloud instance.
					label.uk-text-meta.uk-display-block.uk-margin-small-bottom Your personal API Key
					input.uk-input.uk-text-center.-monospace(v-model="newKey", :class="{ 'uk-form-success' : valid && key === newKey }")
					.uk-alert-danger(v-if="!valid && valid != undefined", uk-alert)
						p.uk-text-center The API-Key is invalid!
				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close.uk-margin-small-right(type='button') Close

					button(v-if="!loading", type='button', @click="setKey", :disabled="loading").uk-button.uk-button-primary.uk-align-right Save
					button(v-else, type='button', disabled).uk-button.uk-button-primary.uk-position-relative.uk-align-right
						.uk-position-small.uk-position-center-left(uk-spinner, uk-icon="icon: spinner; ratio: 0.8")
						| &nbsp;&nbsp;&nbsp;&nbsp; saving

		#view-api-key(uk-modal='center: true')
			.uk-modal-dialog
				button.uk-modal-close-default(type='button', uk-close='')
				.uk-modal-header
					h2.uk-modal-title Marketplace API Key
				.uk-modal-body
					.uk-alert-danger(uk-alert)
						p
							| Your API Key resides in the config.php file which can't be changed here!<br>
							| Please contact your administrator, if the Key appears to be wrong.

					label.uk-text-meta.uk-display-block.uk-margin-small-bottom API Key
					input.uk-input.uk-text-center.-monospace(v-model="key", readonly)

				.uk-modal-footer
					button.uk-button.uk-button-default.uk-modal-close(type='button') Close
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
			openModalEditKey () {
				UIkit.modal('#edit-api-key').toggle();
			},
			openModalViewKey () {
				UIkit.modal('#view-api-key').toggle();
			},
			setKey () {
				console.log("setKey");
				this.$store.dispatch('WRITE_APIKEY', this.newKey);
			}
		},
		mounted () {
			this.$store.dispatch('FETCH_APIKEY');
		},
		computed : {
			key () {
				return this.newKey = this.$store.state.apikey.key;
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