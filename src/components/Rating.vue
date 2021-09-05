<template lang="pug">
	ul.uk-padding-remove.uk-margin-remove.uk-inline-block(uk-tooltip, :title="overall | rating")
		li(v-for="n in stars").uk-inline-block
			span(:class="computedClass", uk-icon="icon: star; ratio: 0.8").star
</template>

<script>
	export default {
		computed: {
			computedClass() {
				let ternary = (this.n <= this.overall) ? '-on' : '-off';
				return ternary;
			}
		},
		props: [
			'rating'
		],
		data () {
			return {
				classOn: "on",
				classOff: "off",
				overall: this.rating.mean,
				stars: 5,
				n: 0
			}
		},
		filters: {
			rating (float) {
				return "&Oslash " + (Math.round(float * 100) / 100) + " stars"
			}
		}
	}
</script>

<style lang="scss" scoped>
	@import "../styles/variables-theme";

	.star {
		margin-right: 3px;

		&.-on {
			color: $global-link-color;
		}

		&.-off {
			opacity: 0.25;
		}
	}
</style>
