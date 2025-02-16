<template>
    <FullCalendar :options="calendarOptions" />
</template>

<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";
import { ref } from "vue";
import dayjs from "@/dayjs";

const calendarOptions = ref({
    timeZone: "local",
    plugins: [dayGridPlugin, interactionPlugin, listPlugin, timeGridPlugin],
    initialView: "dayGridMonth",
    allDaySlot: false,
    eventTimeFormat: {
        hour: "numeric",
        minute: "2-digit",
        meridiem: "short",
    },
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    fixedWeekCount: false,
    showNonCurrentDates: true,
    events: fetchEvents,
});

function fetchEvents(fetchInfo, successCallback, failureCallback) {
    let start = dayjs(fetchInfo.start).format("YYYY-MM-DD");
    let end = dayjs(fetchInfo.end).format("YYYY-MM-DD");

    axios
        .get(route("posts.index", { start, end }))
        .then((response) => {
            const events = response.data.map((event) => ({
                id: event.id,
                title: event.content,
                description: event.body,
                start: event.scheduled_at,
                url: route("posts.edit", event.id),
                backgroundColor:
                    event.status === "scheduled" ? "#ed6413" : "#15db36",
                borderColor:
                    event.status === "scheduled" ? "#ed6413" : "#15db36",
                classNames: ["truncate"],
            }));
            successCallback(events);
        })
        .catch((error) => {
            console.error("Error fetching events:", error);
            failureCallback(error);
        });
}
</script>

<style>
.fc-toolbar-title {
    color: #fff;
}

.fc .fc-daygrid-day.fc-day-today {
    background-color: #374151;
}

.fc-event-title {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.fc-scrollgrid {
    color: rgb(187, 187, 187) !important;
}

.fc-theme-standard td,
.fc-theme-standard th {
    border-color: #111827 !important;
}

.fc-theme-standard .fc-scrollgrid {
    border-color: #111827 !important;
}
.fc .fc-daygrid-day {
    border-color: #111827 !important;
}
</style>
