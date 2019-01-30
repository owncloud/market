<template lang="pug">
	li
		a(href="#", @click.prevent="openModalEditKey") {{ key ? t('Edit API Key') : t('Add API Key') }}

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
			t(string) {
				return this.$gettext(string);
			},
			openModalEditKey () {
				UIkit.modal('#edit-api-key').toggle();
			},
			openModalViewKey () {
				UIkit.modal('#view-api-key').toggle();
			},
			setKey () {
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
