<template>
    <a :href="url" @click="openDocument" target="_blank" class="d-flex flex-row  document col-3 mt-5 project justify-content-between">
        <input type="hidden" name="_token" :value="this.csrfToken">
        <div class="d-flex flex-row justify-content-between">
            <div class="flex-grow-1 flex-shrink-0">
                <h3>{{ name }}</h3>
                <p>{{ description }}</p>
                <div>
                    <p>Created by Artem</p>
<!--                    <p v-else>Last opened {{ this.openedAt }} by Artem</p>-->
                </div>
            </div>
            <div class="d-block align-items-end align-self-end remove">
                <a href="#" class="btn btn-outline-danger h-auto" @click="deleteDocument">Remove</a>
                <a :href="editUrlId" class="btn btn-outline-secondary">Edit</a>
            </div>
        </div>
    </a>
</template>

<script>
import moment from "moment";

export default {
    name: "Document",
    props: {
        id: "",
        name: "",
        description: "",
        url: "",
        lastUserOpened: "",
        lastTimeOpened: ""
    },
    computed: {
        editUrlId: function () {
          return '/editDocument/' + this.id
        },
        csrfToken: function () {
                return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        },
        openedAt: function () {
            //let now = new Date();
            //let opened = Date.parse(this.lastTimeOpened);
            return moment("20120620", "DD").fromNow();
        }
    },
    methods: {
        openDocument: function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            $.ajax({
                url: '/openDocument',
                method: 'POST',
                data: {
                    'id': this.id
                }
            });
        },
        deleteDocument: function () {
            $.ajax({
                url: '/deleteDocument',
                method: 'DELETE',
                data: {
                    'id': this.id
                },
                success: function(){
                    window.location.replace('http://docker.scrum-dashboard/documents');
                }
            });
        }
    }
}
</script>

<style scoped>
    .col {
        height: 12em;
        border-radius: 15px;
        margin-right: 100px;
    }

    .document {
        border-radius: 7px;
        border: 2px solid #74787e;
        box-shadow: 5px 5px 0 0 #adacac;
        display: block;
        text-decoration: none;
        color: #1b1e21;
        margin-top: 3.5em;
        margin-left: 3em;
        margin-right: 3em;
        padding: 12px;
        width: auto;
    }



    .remove {
        z-index: 100;
        margin-left: 20px;
    }
</style>
