<template>
    <div class="chat-app">
        <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"></Conversation>
        <ContactsList :contacts="contacts" @selected="startConversation"></ContactsList>
    </div>
</template>

<script>
    import Conversation from './Conversation';
    import ContactsList from './ContactsList';

    export default {
        props: {
            user:{
                type: Object,
                required: true,
            }
        },
        data(){
            return {
                selectedContact: null,
                messages: [],
                contacts: []
            };
        },
        mounted() {
            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage', (e) => {
                    this.handleIncoming(e.message);
                });

            axios.get('/contacts').then((response) => {
                this.contacts = response.data;
            });
        },
        methods: {
            startConversation(contact)
            {   
                this.updateUnreadCount(contact, true);

                axios.get(`/conversation/${contact.id}`)
                .then((response) => {
                    this.messages = response.data;
                    this.selectedContact = contact;
                });
            },
            saveNewMessage(message){
                this.messages.push(message);
            },
            handleIncoming(message){
                if (this.selectedContact && message.from == this.selectedContact.id)
                {
                    this.saveNewMessage(message);
                    return;
                }

                this.updateUnreadCount(message.from_contact, false);
            },
            updateUnreadCount(contact, reset){
                this.contacts = this.contacts.map((singleContact) => {
                    if (singleContact.id !== contact.id){
                        return singleContact;
                    }

                    if (reset){
                        singleContact.unread = 0;
                    }
                    else{
                        singleContact.unread++;
                    }

                    return singleContact;
                })
            }
        },
        components: {ContactsList, Conversation}

    }
</script>

<style lang="scss" scoped>
.chat-app {
    display: flex;
    margin-bottom: 1px black;
}
</style>