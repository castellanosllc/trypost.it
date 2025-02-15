// dayjs
import dayjs from "dayjs";
import utc from "dayjs/plugin/utc";
import timezone from "dayjs/plugin/timezone";
import relativeTime from "dayjs/plugin/relativeTime";
import calendar from "dayjs/plugin/calendar";
import duration from "dayjs/plugin/duration";
import updateLocale from "dayjs/plugin/updateLocale";
dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(calendar);
dayjs.extend(relativeTime);
dayjs.extend(duration);
dayjs.extend(updateLocale);

import "dayjs/locale/da";
import "dayjs/locale/de";
import "dayjs/locale/en";
import "dayjs/locale/es";
import "dayjs/locale/fr";
import "dayjs/locale/it";
import "dayjs/locale/nl";
import "dayjs/locale/hu";
import "dayjs/locale/pt-br";
import "dayjs/locale/ru";
import "dayjs/locale/tr";

export default dayjs;
