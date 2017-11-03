import showdown from "showdown";

export default {
	methods: {
		t (string, interpolation) {
			if (!interpolation) {
				return this.$gettext(string);
			}
			else {
				// %{interplate} with object
				return this.$gettextInterpolate(string, interpolation);
			}
		},
		markdown (string) {
			let converter = new showdown.Converter();
			let markdown  = converter.makeHtml(string);

			return markdown;
		}
	}
}
