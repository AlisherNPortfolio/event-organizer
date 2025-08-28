<template>
<div id="dashboard-app" class="space-y-6">
    <DashboardHeader :username="user.name" :rating="userRating"></DashboardHeader>

    <DashboardStatistics
        :upcomingEventsCount="upcomingEventsCount"
        :completedEventsCount="completedEventsCount"
        :totalParticipants="totalParticipants"
        :userRating="userRating"
    ></DashboardStatistics>

    <DashboardQuickActions></DashboardQuickActions>

    <DashboardRecentEvents
        :user-id="user.id"
        :user-events="userEvents"
    ></DashboardRecentEvents>

    <DashboardCharts></DashboardCharts>
</div>
</template>
<script setup>
import DashboardHeader from './DashboardHeader.vue';
import DashboardStatistics from './DashboardStatistics.vue';
import DashboardQuickActions from './DashboardQuickActions.vue';
import DashboardRecentEvents from './DashboardRecentEvents.vue';
import DashboardCharts from './DashboardCharts.vue';
const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    userRating: {
        required: true
    },
    userEvents: {
        type: Array,
        required: true
    },
    upcomingEventsCount: {
        type: Number,
        required: true
    },
    completedEventsCount: {
        type: Number,
        required: true
    },
    totalParticipants: {
        type: Number,
        required: true
    },
});

console.log(props);
</script>
