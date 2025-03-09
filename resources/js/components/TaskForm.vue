<template>
    <v-container class="mt-6">
      <v-row justify="center">
        <v-col cols="12" md="8">
          <v-card class="pa-5">
            <v-card-title class="text-h5 text-center">
              {{ isEditing ? "Edit Task" : "Create Task" }}
            </v-card-title>
  
            <v-card-text>
              <v-form @submit.prevent="submitTask">
                <!-- Title Field -->
                <v-text-field 
                  v-model="title" 
                  label="Task Title" 
                  required
                  outlined
                  class="mb-3"
                  :error-messages="errors.title"
                ></v-text-field>
  
                <!-- Description Field -->
                <v-textarea 
                  v-model="description" 
                  label="Task Description" 
                  outlined
                  class="mb-3"
                  :error-messages="errors.description"
                ></v-textarea>
  
                <!-- File Upload -->
                <v-file-input 
                  label="Upload Image (Optional)" 
                  accept="image/*" 
                  outlined 
                  class="mb-3"
                  @change="handleFileUpload"
                  :error-messages="errors.image"
                ></v-file-input>
  
                <!-- Submit Button -->
                <v-btn type="submit" color="blue" block>
                  <v-icon left>{{ isEditing ? "mdi-pencil" : "mdi-plus" }}</v-icon>
                  {{ isEditing ? "Update Task" : "Create Task" }}
                </v-btn>
  
                <!-- Error Message -->
                <p v-if="error" class="text-red mt-3 text-center">{{ error }}</p>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>
  
  <script>
  import axios from "axios";
  import { defineComponent } from "vue";
  import { VContainer, VRow, VCol, VCard, VCardTitle, VCardText, VForm, VTextField, VTextarea, VFileInput, VBtn, VIcon } from "vuetify/components";
  
  export default defineComponent({
    components: {
      VContainer,
      VRow,
      VCol,
      VCard,
      VCardTitle,
      VCardText,
      VForm,
      VTextField,
      VTextarea,
      VFileInput,
      VBtn,
      VIcon,
    },
    props: ["task"],
    data() {
      return {
        title: this.task?.title || "",
        description: this.task?.description || "",
        image: null,
        error: "",
        errors: {}, // Store validation errors
        isEditing: !!this.task,
      };
    },
    methods: {
      handleFileUpload(event) {
        this.image = event.target.files[0];
      },
      async submitTask() {
        this.error = "";
        this.errors = {}; // Clear previous errors
  
        try {
          let formData = new FormData();
          formData.append("title", this.title);
          formData.append("description", this.description);
          if (this.image) {
            formData.append("image", this.image);
          }
  
          let response;
          if (this.isEditing) {
            formData.append("_method", "PUT"); // Fix for Laravel PUT request
            response = await axios.post(`/tasks/${this.task.id}`, formData, {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "Content-Type": "multipart/form-data",
              },
            });
          } else {
            response = await axios.post("/tasks", formData, {
              headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                "Content-Type": "multipart/form-data",
              },
            });
          }
  
          this.$emit("taskUpdated", response.data.task); // Notify parent component
        } catch (err) {
          if (err.response && err.response.status === 422) {
            this.errors = err.response.data.errors; // Laravel validation errors
          } else {
            this.error = "Error saving task. Please try again.";
          }
        }
      },
    },
  });
  </script>
  
  <style>
  /* Adds spacing between the form and task list */
  .mt-6 {
    margin-top: 40px;
  }
  </style>
  