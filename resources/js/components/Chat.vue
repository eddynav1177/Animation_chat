<template>
    <v-layout row>
        <v-flex xs12 sm12 offset-sm3>
            <v-card class='mb-4' dark=''>
                <v-list>
                    <v-subheader>
                        Group chat
                    </v-subheader>
                    <div v-for="(item, index) in allMessages" :key="index">
                        <v-chip :color="(user === auth_user) ? 'red' : 'blue'" text-color="white">
                            <div v-if="item != ''">
                                {{ item }}
                            </div>
                        </v-chip>
                        <!-- <p>Envoyé par : {{ sender }}, le {{ created_at }}</p> -->
                    </div>
                    <v-divider></v-divider>
                </v-list>
            </v-card>
        </v-flex>
        <v-footer height="auto" fixed color="grey">
            <v-layout row>
                <v-flex xs6 justify-center align-center>
                    <v-text-field
                        row=2
                        label='Enter message'
                        single-line
                        v-model="content"
                        @keyup.enter="sendMessage"

                    >

                    </v-text-field>
                </v-flex>
                <v-flex xs2>
                    <v-btn dark class="mt-3 white--text" small color="green" @click="sendMessage">Send</v-btn>
                </v-flex>
            </v-layout>
        </v-footer>
    </v-layout>
</template>

<script>
    export default {
        props: ['auth_user'],
        data () {
            return {
                content: null,
                allMessages: [],
                message: [],
                created_at: '',
                sender: '',
                destination : _.last( window.location.pathname.split( '/' ) ),
                // user : ''

            }
        },
        mounted() {
            /*this.auth_user = this.auth_user
            console.log('auth_user : ' + auth_user);*/
            Echo.private('chat'+this.auth_user)
            .listen('NewMessageEvent', function (e) {
                this.allMessages.push(e.content)
            })
        },
        /*created () {
            // user_id = this.auth_user.id
            console.log(this.auth_user.id)
        },*/
        methods: {
            sendMessage() {
                if (!this.content) {
                    return alert('Entrez un message');
                }

                // this.allMessages.push(this.message);
                // axios.post('/api/message/chat/'+this.auth_user.id, {mesage: this.message})
                axios.post('/api/message/chat/'+this.destination, {content: this.content})
                    .then(response => {
                        console.log('response : ' + response.data);
                        this.content = '';
                        // this.allMessages.push(response.m)
                        // this.created_at = response.data.message.created_at;

                        this.fetchMessages();
                        console.log('this.messages : ' + this.messages);
                    })
                    .catch((err) => console.log(err.response));
            },
            scrollToEnd() {
                window.scrollTo(0, 99999);
            },
            fetchMessages() {
                axios.get('/api/message/view_message/'+this.destination, this.content)
                .then(response => {
                    // this.allMessages    = response.data.messages;
                    this.allMessages    = response.data.messages.message;
                    console.log(this.allMessages);
                    this.user = response.data.user.name
                    console.log('user : ' + this.user);
                });
            }
        },

        created() {
            this.fetchMessages();
            console.log(this.auth_user.id);
            var destination = _.last( window.location.pathname.split( '/' ) );
            console.log('id : ' + destination)
        }

    }
</script>
