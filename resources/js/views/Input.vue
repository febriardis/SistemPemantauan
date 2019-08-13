<template>
    <div class="input_data">
        <h1>Form Percobaan</h1>
        <!-- <form ></form> -->
        <v-text-field
            v-model="temperature"
            label="Nilai Suhu"    
        ></v-text-field>
        <v-text-field
            v-model="ph"
            label="Nilai Keasaman (pH)"
        ></v-text-field>
        <v-text-field
            v-model="turbidity"
            label="Nilai Kekeruhan"
        ></v-text-field>
        <v-btn
            @click="postData"
            color="primary"
            block
        >
        Simpan Data</v-btn>
    </div>
</template>

<script>
import axios from 'axios'
export default {
    data(){
        return {
            temperature: '',
            ph: '',
            turbidity: ''
        }
    },
    methods:{
        postData(){
            axios.post('/createManual', {
                temperature: this.temperature,
                ph: this.ph,
                turbidity: this.turbidity
            })
            .then(response => {
                console.log(response.data)
                this.$swal('Sukses','Data berhasil disimpan!');
                return this.$router.push({name:'home'})
            })
            .catch(error => {
                console.log(error.response)
            })
        }
    }
}
</script>
