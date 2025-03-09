<template>
    <v-app>
      <!-- Navbar -->
      <v-app-bar color="blue" dark>
        <v-container class="d-flex justify-space-between align-center">
          <v-toolbar-title>Task Manager</v-toolbar-title>
  
          <div v-if="user">
            <span class="mr-3">Welcome, {{ user.name }}</span>
            <v-btn color="red" @click="logout">
              <v-icon left>mdi-logout</v-icon> Logout
            </v-btn>
          </div>
        </v-container>
      </v-app-bar>
  
      <!-- Task List Section -->
      <v-container class="mt-6">
        <v-row justify="center">
          <v-col cols="12" md="8">
            <v-card class="pa-4">
              <v-card-title class="text-h5 text-center">My Tasks</v-card-title>
  
              <v-btn color="blue" class="mb-4" @click="showForm = true">
                <v-icon left>mdi-plus</v-icon> Create Task
              </v-btn>
  
              <!-- Task Form -->
              <TaskForm v-if="showForm" @taskUpdated="fetchTasks" :task="selectedTask" @closeForm="showForm = false" />
  
              <!-- Task List -->
              <v-list>
                <v-list-item v-for="task in tasks" :key="task.id">
                  <v-list-item-title class="font-weight-bold">
                    {{ task.title }}
                  </v-list-item-title>
                  <v-list-item-subtitle>
                    {{ task.description || "No description available" }}
                  </v-list-item-subtitle>
  
                  <!-- Task Image -->
                  <v-row v-if="task.image" class="mt-2">
                    <v-col cols="12">
                      <v-img :src="getImageUrl(task.image)" max-height="200" contain></v-img>
                    </v-col>
                    <v-col cols="12">
                      <v-btn color="primary" block :href="getImageUrl(task.image)" target="_blank" download>
                        <v-icon left>mdi-download</v-icon> Download Image
                      </v-btn>
                    </v-col>
                  </v-row>
  
                  <!-- Actions -->
                  <v-row class="mt-3">
                    <v-col cols="6">
                      <v-btn color="green" block @click="editTask(task)">
                        <v-icon left>mdi-pencil</v-icon> Edit
                      </v-btn>
                    </v-col>
                    <v-col cols="6">
                      <v-btn color="red" block @click="deleteTask(task.id)">
                        <v-icon left>mdi-delete</v-icon> Delete
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-list-item>
              </v-list>
  
              <p v-if="tasks.length === 0" class="text-center text-grey mt-3">
                No tasks available. Click "Create Task" to add one.
              </p>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-app>
  </template>
  
  <script>
  import axios from "axios";
  import TaskForm from "./TaskForm.vue";
  import { defineComponent } from "vue";
  import { VContainer, VRow, VCol, VCard, VCardTitle, VBtn, VIcon, VList, VListItem, VListItemTitle, VListItemSubtitle, VImg } from "vuetify/components";
  
  export default defineComponent({
    components: {
      TaskForm,
      VContainer,
      VRow,
      VCol,
      VCard,
      VCardTitle,
      VBtn,
      VIcon,
      VList,
      VListItem,
      VListItemTitle,
      VListItemSubtitle,
      VImg,
    },
    data() {
      return { 
        tasks: [], 
        showForm: false, 
        selectedTask: null,
        user: null, // Store the authenticated user's data
      };
    },
    async mounted() {
      this.fetchUser();
      this.fetchTasks();
    },
    methods: {
      async fetchUser() {
        try {
          const response = await axios.get("/api/user", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
          });
          this.user = response.data;
        } catch (error) {
          console.error("Error fetching user:", error);
        }
      },
      async fetchTasks() {
        try {
          const res = await axios.get("/tasks", {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
          });
          this.tasks = res.data;
          console.log("Fetched tasks:", this.tasks);
        } catch (error) {
          console.error("Error fetching tasks:", error);
        }
      },
      async deleteTask(id) {
        try {
          await axios.delete(`/tasks/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
          });
          this.fetchTasks();
        } catch (error) {
          console.error("Error deleting task:", error);
        }
      },
      editTask(task) {
        this.selectedTask = task;
        this.showForm = true;
      },
      async logout() {
        try {
          await axios.post("/logout", {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
          });
  
          localStorage.removeItem("token");
          this.$router.push("/login");
        } catch (error) {
          console.error("Logout failed:", error);
        }
      },
      getImageUrl(imagePath) {
        if (!imagePath) return null;
        console.log("Image Path:", imagePath);
        return `http://task-manager.test/storage/${imagePath}`;
      },
    },
  });
  </script>
  
  <style>
  .v-btn {
    text-transform: none;
  }
  </style>
  