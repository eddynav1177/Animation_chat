<template>
    <v-layout row>
        <v-flex xs12 sm12 offset-sm3>
            <v-card class='mb-4' dark=''>
                <v-list>
                    <v-subheader>
                        Group chat
                    </v-subheader>
                    <div v-for="(item, index) in allMessages" :key="index">
                        <v-chip :color="(user === auth_user) ? 'red' : 'blue'" text-color="green">
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
                        label='Entrez un message'
                        single-line
                        v-model="body"
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
                body: null,
                csrfToken: null,
                allMessages: [],
                message: [],
                created_at: '',
                sender: '',
                destination: window.location.pathname.split('/').slice(-2, -1)[0],
                fack_user: window.location.pathname.split('/').pop(),
            }
        },
        mounted() {
            Echo.private('chat')
            .listen('NewMessageEvent', function (e) {
                this.allMessages.push(e.body),
                console.log('send')
            })
            console.log('test')
        },
        methods: {
            sendMessage() {
                if (!this.body) {
                    return alert('Entrez un message');
                }

                axios.post('/api/message/chat/'+this.destination+'/'+this.fack_user, {body: this.body})
                    .then(response => {
                        console.log('response2 : ' + response.data);
                        this.body = '';
                        this.fetchMessages();
                        setTimeout(this.scrollToEnd, 100)
                    })
                    .catch((err) => console.log('err : ' + err.response));
            },
            scrollToEnd() {
                window.scrollTo(0, 99999);
            },
            fetchMessages() {
                axios.get('/api/message/view_message/'+this.destination, this.body)
                .then(response => {
                    this.allMessages    = response.data.messages;
                    this.user           = response.data.user.name;
                });
            }
        },

        created() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').content
            this.fetchMessages();
            console.log('destination : ' + this.destination+ ', fack_user : ' + this.fack_user)
        }

    }
</script>
