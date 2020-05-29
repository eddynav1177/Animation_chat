<template>
    <v-layout row>
        <v-flex xs12 sm12 offset-sm3>
            <v-card class='mb-4' dark=''>
                <v-list>
                    <v-subheader>
                        Group chat
                    </v-subheader>
                    <v-divider></v-divider>
                    <v-list-title class="p-3" v-for="(item, index) in allMessages" :key="index">
                        <v-layout
                            :align-end="(index%2==0)" column>
                            <v-flex>
                                <v-layout column>
                                    <v-flex>
                                        <span class="small font-italic">Envoyeur</span>
                                    </v-flex>
                                    <v-flex>
                                        <v-chip
                                            :color="(index%2==0)?'red':'green'" text-color="white">
                                            <v-list-title-content>{{item.message}}</v-list-title-content>
                                        </v-chip>
                                    </v-flex>
                                    <v-flex class="caption font-italic">
                                        2020-10-16
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                    </v-list-title>
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
                        v-model="message"
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
        data () {
            return {
                mesage: null,
                allMessages: []
            }
        },
        mounted() {
            console.log('test');
        },
        methods: {
            sendMessage() {
                if (!this.message) {
                    return alert('Entrez un message');
                }

                // this.allMessages.push(this.message);
                axios.post('/api/message/chat/1', {mesage: this.message})
                    .then(response => {
                        console.log(response.data);
                    });
            },
        },
        fechMessages() {
            axios.get('/messages', this.message)
                .then(response => {
                    this.allMessages    = response.data;
                });
        }
    }
</script>
