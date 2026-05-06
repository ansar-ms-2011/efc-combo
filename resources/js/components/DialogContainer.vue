<template>
    <component
        v-for="(dialog, index) in dialogs"
        :key="index"
        :is="dialog.component"
        v-bind="dialog.props"
        @close="close(dialog, $event)"
    />
</template>

<script setup>
    import dialog from "../services/dialog"

    const dialogs = dialog.state.dialogs

    function close(d, result) {
        dialog.close(d, result)
    }
</script>
