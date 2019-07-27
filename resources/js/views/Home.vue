<template>
    <div class="home">
        <div style="margin-bottom:10px;">
            <h4 class="display-1" style="float:left">Pemantauan Air Akuarium</h4>
            <v-card style="float:right;margin:5px 0px;padding:6px 6px 3px 6px">
                <v-icon>event</v-icon>
                <div v-if="data.created_at!=null" style="float:right;margin:2px"><b>{{moment(data.created_at).format('dddd, DD MMMM YYYY')}} | {{moment(data.created_at).format('HH:mm')}}</b></div>
            </v-card>
            <div class="clear"></div>
        </div>
        <v-card class="elevation-10" style="padding:20px">
            <v-layout row wrap>
                <v-flex md6 sm12 xs12>
                    <h6 class="title"><b>Data Kriteria:</b></h6>
                    <table style="margin: 8px 0px">
                        <thead>
                            <tr bgcolor="#EEE" height="40">
                                <th width="280">Kriteria</th>
                                <th width="120">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr height="40">
                                <td style="padding:0px 20px">Keasaman (pH)</td>
                                <td style="text-align:center">{{data.ph}}</td>
                            </tr>
                            <tr height="40">
                                <td style="padding:0px 20px">Suhu</td>
                                <td style="text-align:center">{{data.temperature}}</td>
                            </tr>
                            <tr height="40">
                                <td style="padding:0px 20px">Kekeruhan</td>
                                <td style="text-align:center">{{data.turbidity}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <v-card style="margin:10px 30px 20px 0px; padding:10px 20px">
                        <h6 class="title"><b>Keterangan:</b>&nbsp;{{data.status}}</h6>
                    </v-card>
                </v-flex>
                <v-flex md6 sm12 xs12>
                    <h6 class="title"><b>lorem ipsum</b></h6>
                    
                    
                </v-flex>
            </v-layout>
        </v-card>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
export default {
    data(){
        return {
            data: []
        }
    },

    methods: {
        moment,
      
        getData(){
            axios.get('/show/latest')
            .then(response => {
                this.data = response.data.data
            })
            .catch(error => {
                console.log(error.response)
            })
        }
    },

    // updated(){
    //     this.getData()
    // },

    mounted(){
        this.getData()
    }
}
</script>


<style>
    td{
        border-bottom:1px solid #E0E0E0;
    }
</style>
