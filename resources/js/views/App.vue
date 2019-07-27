<template>
    <v-app>
        <v-toolbar fixed dark color="light-blue darken-1">
            <v-toolbar-side-icon class="hidden-md-and-up"></v-toolbar-side-icon>
            <v-toolbar-title>Aplikasi Pemantauan</v-toolbar-title>
        </v-toolbar>
        <v-content style="margin-top:65px">
            <v-container fluid>
                <v-layout row wrap>
                    <v-flex md3 sm12 xs12 class="hidden-sm-and-down">
                        <!-- navigasi -->
                        <Sidebar/>
                        <!-- /navigasi -->
                    </v-flex>

                    <v-flex md9 sm12 xs12>
                        <!-- contet -->
                        <router-view></router-view>
                        <!-- /content -->
                    </v-flex>
                </v-layout>
                
            </v-container>
        </v-content>
        <v-footer app></v-footer>
    </v-app>
</template>

<script>
    import Sidebar from '../components/SideBar'
    export default {
        name:'app',
        components:{
            Sidebar
        }, 
    }
</script>

