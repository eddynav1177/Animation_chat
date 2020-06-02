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

            }
        },
        mounted() {
            window.Echo.private('chat')
            .listen('NewMessageEvent', function (e) {
                this.allMessages.push(e.content),
                console.log('send')
            })
        },
        methods: {
            sendMessage() {
                if (!this.content) {
                    return alert('Entrez un message');
                }

                axios.post('/api/message/chat/'+this.destination, {content: this.content})
                    .then(response => {
                        console.log('response2 : ' + response.data);
                        this.content = '';
                        // this.allMessages.push(response.data.message)
                        // console.log('allMessages : ' + this.allMessages);
                        // this.created_at = response.data.message.created_at;
                        this.fetchMessages();
                        setTimeout(this.scrollToEnd, 100)
                    })
                    .catch((err) => console.log('err : ' + err.response));
            },
            scrollToEnd() {
                window.scrollTo(0, 99999);
            },
            fetchMessages() {
                axios.get('/api/message/view_message/'+this.destination, this.content)
                .then(response => {
                    this.allMessages    = response.data.messages.message;
                    this.user = response.data.user.name
                });
            }
        },

        created() {
            this.fetchMessages();
            var destination = _.last( window.location.pathname.split( '/' ) );
        }

    }
</script>
