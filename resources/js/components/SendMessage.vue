<template>
    <v-layout row>
        <v-flex xs12 sm6 offset-sm3>
            <v-card class='mb-4' dark=''>
                <v-list>
                    <v-subheader>
                        Group chat
                    </v-subheader>
                    <v-divider></v-divider>
                    <v-list-title class="p-3" v-for="(item, index) in 3" :key="index">
                        <v-layout :align-end="(index%2==0)" column>
                            <v-flex>
                                <v-layout column>
                                    <v-flex>
                                        <span class="small font-italic">Envoyeur</span>
                                    </v-flex>
                                    <v-flex>
                                        <v-chip :color="(index%2==0) ? 'red' : 'green'" text-color="white">
                                            <v-list-title-content>Simple message</v-list-title-content>
                                        </v-chip>
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
                    <v-text-field row=2 label='Enter message' single-line>

                    </v-text-field>
                </v-flex>
                <v-flex xs2>
                    <v-btn dark class="mt-3 white--text" small color="green">Send</v-btn>
                </v-flex>
            </v-layout>
        </v-footer>
    </v-layout>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.22222')
        }
    }
</script>
