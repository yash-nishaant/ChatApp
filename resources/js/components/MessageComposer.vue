<template>
    <div class="composer">
        <textarea v-model="message" @keydown.enter="send" placeholder="Type your message..." rows="1"></textarea>    
    </div>
</template>

<script>

export default {
    data(){
        return {
            message: '',
        };
    },
    methods: {
        send(e){
            e.preventDefault();
            if (this.message == '')
            {
                return;
            }
            this.$emit('send', this.message);
            this.message = '';
        },
    }, 
}
</script>

<style lang="scss" scoped>
    .composer textarea {
    width: 96%;
    margin: 10px;
    resize: none;
    border-radius: 3px;
    border: 1px solid lightgray;
    padding: 3px;
}
    .composer button {
        padding-top: 0%;
    }
</style>
