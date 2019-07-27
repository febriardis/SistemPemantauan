<template>
    <div class="history">
        <h4 class="display-1">Riwayat Pemantauan Air Akuarium</h4>
        <br>
        <v-data-table
            :headers="headers"
            :items="(index,data)"
            class="elevation-5"
            :rows-per-page-items="rowsPerPage"
            disable-initial-sort
        >
            <template v-slot:items="props">
                <td class="text-xs-center">{{ props.index+1 }}</td>
                <td>{{moment(props.item.created_at).format('dddd, DD MMMM YYYY')}} </td>
                <td>{{moment(props.item.created_at).format('HH:mm')}}</td>
                <td class="text-xs-center">{{ props.item.temperature }}</td>
                <td class="text-xs-center">{{ props.item.ph }}</td>
                <td class="text-xs-center">{{ props.item.turbidity }}</td>
                <td>{{ props.item.status }}</td>
            </template>
        </v-data-table>
    </div>
</template>

<script>
import Axios from 'axios';
import moment from 'moment';
    export default {
        data () {
            return {
                no:1,
                rowsPerPage: [10, 25, 50, {"text":"$vuetify.dataIterator.rowsPerPageAll","value":-1}],
                headers: [
                    { text: 'No',align: 'center', sortable: false, value: 'name'},
                    { text: 'Tanggal', value: 'created_at' },
                    { text: 'Waktu', value: 'creted_at' },
                    { text: 'Keasaman (pH)', value: 'ph' },
                    { text: 'Suhu', value: 'temperature' },
                    { text: 'Kekeruhan', value: 'turbidity' },
                    { text: 'Keterangan', value: 'status' }
                ],
                index: [],
                data: []
            }
        },

        methods:{
            moment,
            getHistory(){
                Axios.get('/show/all')
                .then(response=>{
                    this.data = response.data.data
                })
                .catch(error=>{
                    console.log(error.response)
                })
            }
        },

        // updated(){
        //     this.getHistory()
        // },

        mounted(){
            this.getHistory()
        }
    }
</script>

