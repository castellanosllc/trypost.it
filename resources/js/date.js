import dayjs from "@/dayjs";
const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

export default {
    formatDate(date) {
        return dayjs.utc(date).tz(timezone).format("MMM D, YYYY");
    },

    formatDateTime(date) {
        return dayjs.utc(date).tz(timezone).format("MMM D, YYYY h:mm A");
    },

    formatTime(date) {
        return dayjs.utc(date).tz(timezone).format("HH:mm A");
    },

    formatDateTimeForApi(date) {
        return dayjs.utc(date).tz('utc').format("YYYY-MM-DD HH:mm:ss");
    },

    diffForHumans(date) {
        let localDate = dayjs
            .utc(date)
            .tz(timezone)
            .format("YYYY-MM-DD HH:mm:ss");
        return dayjs().to(dayjs(localDate));
    },

    formatResponseDelay(seconds) {
        return dayjs().add(seconds, "seconds").fromNow(true);
    },

    userTime(t) {
        return dayjs().tz(t).format("h:mm A");
    }
};
