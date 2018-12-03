define({ "api": [
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "E__teamwork_upload_doc_main_js",
    "groupTitle": "E__teamwork_upload_doc_main_js",
    "name": ""
  },
  {
    "type": "get",
    "url": "/checkfile",
    "title": "验证文件是否存在，用于极速秒传",
    "name": "checkfile",
    "group": "Upload",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "md5",
            "description": "<p>文件MD5值.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\n{\"errcode\":\"0\",\"message\":\"ok\",\"data\":{\"exits\":1}}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\"errcode\":400,\"message\":\"获取失败\"}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./application/index/controller/Upload.php",
    "groupTitle": "Upload"
  },
  {
    "type": "post",
    "url": "/index",
    "title": "上传文件，支持断点续传",
    "name": "index",
    "group": "Upload",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "File",
            "optional": false,
            "field": "file",
            "description": "<p>文件名,form-data上传.</p>"
          },
          {
            "group": "Parameter",
            "type": "integer",
            "optional": false,
            "field": "chunk",
            "description": "<p>分片.</p>"
          },
          {
            "group": "Parameter",
            "type": "integer",
            "optional": false,
            "field": "chunks",
            "description": "<p>分片总数.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "check_md5",
            "description": "<p>分片MD5.</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "name",
            "description": "<p>文件名.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "\n{\"errcode\":\"0\",\"message\":\"上传成功\",\"data\":\"http:\\/\\/47.104.218.167:892\\/upload\\/2018\\/12\\/02\\/15437415757052.jpg\"}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\"errcode\":400,\"message\":\"上传失败\"}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./application/index/controller/Upload.php",
    "groupTitle": "Upload"
  }
] });
