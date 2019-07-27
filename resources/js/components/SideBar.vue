<template>
    <div class="sidebar">
       <v-navigation-drawer permanent fixed style="margin-left:30px; top:92px; z-index:0;" height="250" width="260">
            <v-toolbar height="50">
                <h5 class="headline"></h5>
                <v-divider style="border:1px solid #E0E0E0"></v-divider>
            </v-toolbar>

            <v-list dense class="pt-0" style="margin-top:8px">
                <v-list-tile @click="linkHome">
                    <v-list-tile-action>
                        <v-icon>dashboard</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Dashboard</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="linkGrafik">
                    <v-list-tile-action>
                        <v-icon>show_chart</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Grafik Pemantauan</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="linkHistory">
                    <v-list-tile-action>
                        <v-icon>history</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Riwayat</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
                <v-list-tile @click="linkInformation">
                    <v-list-tile-action>
                        <v-icon>announcement</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>Informasi</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-navigation-drawer> 
    </div>
</template>

<script>
export default {
    methods:{
        linkHome(){
            return this.$router.push({name: 'home'})
        },
        
        linkGrafik(){
            return this.$router.push({name: 'grafik'})
        },
        linkHistory(){
            return this.$router.push({name: 'history'})
        },
        
        linkInformation(){
            return this.$router.push({name: 'information'})
        }
    }     
}
</script>
