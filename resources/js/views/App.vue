<template>
    <v-app>
        <div>
    <v-toolbar>
      <v-toolbar-title>Title</v-toolbar-title>

      <v-spacer></v-spacer>

      <v-toolbar-items>
        <v-btn text>Link 1</v-btn>
        <v-btn text>Link 2</v-btn>
        <v-btn text>Link 3</v-btn>
      </v-toolbar-items>

      <template v-if="$vuetify.breakpoint.smAndUp">
        <v-btn icon>
          <v-icon>mdi-export-variant</v-icon>
        </v-btn>
        <v-btn icon>
          <v-icon>mdi-delete-circle</v-icon>
        </v-btn>
        <v-btn icon>
          <v-icon>mdi-plus-circle</v-icon>
        </v-btn>
      </template>
    </v-toolbar>
  </div>

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

