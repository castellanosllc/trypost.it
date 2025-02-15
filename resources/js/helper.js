export default {
    kFormatter(num) {
        return Math.abs(num) > 999
            ? Math.sign(num) * (Math.abs(num) / 1000).toFixed(1) + "k"
            : Math.sign(num) * Math.abs(num);
    },

    getUserFirstName(name) {
        const [first, last] = name.split(" ");
        return first;
    },

    getUserFirstLetterName(name) {},

    generateAvatar(name, color = "black") {
        const letter = name.charAt(0).toUpperCase();

        const canvas = document.createElement("canvas");
        const context = canvas.getContext("2d");

        canvas.width = 200;
        canvas.height = 200;

        // Draw background
        context.fillStyle = color;
        context.fillRect(0, 0, canvas.width, canvas.height);

        // Draw text
        context.font = "600 80px Inter";
        context.fillStyle = "white";
        context.textAlign = "center";
        context.textBaseline = "middle";
        context.fillText(letter, canvas.width / 2, canvas.height / 2);

        return canvas.toDataURL("image/png");
    },

    capitalize(value) {
        if (!value) return "";
        return value
            .split(" ")
            .map((val) => val.charAt(0).toUpperCase() + val.slice(1))
            .join(" ");
    },

    lowerCase(value) {
        if (!value) return "";
        return value.toLowerCase();
    },

    copyToClipboard(url, message = null) {
        const clipboardData =
            event.clipboardData ||
            window.clipboardData ||
            event.originalEvent?.clipboardData ||
            navigator.clipboard;

        clipboardData.writeText(url);

        // Show flash message
        if (message) {
            usePage().props.flash.bannerStyle = "success";
            usePage().props.flash.banner = message;
        }
    },

    getYouTubeVideoIdFromUrl(url) {
        const regExp =
            /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return match && match[2].length === 11 ? match[2] : undefined;
    },

    getVimeoIdFromUrl(url) {
        const match = /vimeo.*\/(\d+)/i.exec(url);
        if (match) {
            return match[1];
        }
    },
};
