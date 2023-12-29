<?php

namespace App\Http\Controllers\API\V1\Mahasiswa\UploadFile;

use App\Http\Controllers\Controller;
use App\Models\Api\Mahasiswa\UploadFile\ApiUploadFileModel;
use App\Models\Api\Mahasiswa\Profile\ApiAccountModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class ApiUploadFileController extends Controller
{

    public function index(Request $request)
    {
        // Get api_token from the request body
        $apiToken = $request->input('api_token');

        // Check if api_token is provided
        if (empty($apiToken)) {
            $response = [
                'status' => false,
                'message' => 'Missing api_token in the request body.',
                'data' => null,
            ];
            return response()->json($response, 400); // 400 Bad Request
        }

        $userId = $request->input('user_id');
        $user = ApiAccountModel::getById($userId);

        // Check if the user exists
        if ($user != null) {
            // Attempt to authenticate the user based on api_token
            if ($user->api_token != null) {
                // Authorize
                $isAuthorized = ApiAccountModel::authorize('R', $userId);

                if (!$isAuthorized) {
                    $response = [
                        'status' => false,
                        'message' => 'Akses tidak sah!',
                        'data' => null,
                    ];
                    return response()->json($response, 403);
                } else {
                    // Check if the provided api_token matches the user's api_token
                    if ($user->api_token == $apiToken) {
                        // Data

                        try {
                            // get data kelompok
                            $file_mhs = ApiUploadFileModel::fileMHS($user ->user_id);

                            // data
                            $data = ['file_mhs' => $file_mhs];

                            // response
                            return response()->json(['status' => true, 'message' => "Berhasil mendapatkan data.", 'data' => $data]);
                        } catch (\Exception $e) {
                            // handle unexpected errors
                            return response()->json(['status' => false, 'message' => $e->getMessage()]);
                        }
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Token tidak valid!',
                            'data' => null,
                        ];
                        return response()->json($response, 401); // 401 Unauthorized
                    }
                }
            }
        } else {
            // User not found or api_token is null
            $response = [
                'status' => false,
                'message' => 'Pengguna tidak ditemukan!',
                'data' => null,
            ];
            return response()->json($response, 404); // 401 Unauthorized
        }
    }

    public function uploadMakalahProcess(Request $request)
    {
        // Get api_token from the request body
        $apiToken = $request->input('api_token');

        // Check if api_token is provided
        if (empty($apiToken)) {
            $response = [
                'status' => false,
                'message' => 'Missing api_token in the request body.',
                'data' => null,
            ];
            return response()->json($response, 400); // 400 Bad Request
        }

        $userId = $request->input('user_id');
        $user = ApiAccountModel::getById($userId);

        // Check if the user exists
        if ($user != null) {
            // Attempt to authenticate the user based on api_token
            if ($user->api_token != null) {
                // Authorize
                $isAuthorized = ApiAccountModel::authorize('C', $userId);

                if (!$isAuthorized) {
                    $response = [
                        'status' => false,
                        'message' => 'Akses tidak sah!',
                        'data' => null,
                    ];
                    return response()->json($response, 403);
                } else {
                    // Check if the provided api_token matches the user's api_token
                    if ($user->api_token == $apiToken) {
                        // Validate the request
                        $validator = Validator::make($request->all(), [
                            'makalah' => 'required|file|mimes:pdf|max:10240', // Example: Allow only PDF files with a maximum size of 10MB
                            'id_mahasiswa' => 'required|exists:kelompok_mhs,id_mahasiswa', // Replace 'your_kel_mhs_table' with the actual table name
                        ]);

                        // Check if validation fails
                        if ($validator->fails()) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Validation error',
                                'errors' => $validator->errors(),
                            ], 400);
                        }

                        // Upload path
                        $uploadPath = '/file/mahasiswa/makalah';

                        // Upload FOTO
                        if ($request->hasFile('makalah')) {
                            $file = $request->file('makalah');

                            // Generate a unique file name
                            $newFileName = 'makalah-' . Str::slug($user->user_name, '-') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                            // Check if the folder exists, if not, create it
                            if (!is_dir(public_path($uploadPath))) {
                                mkdir(public_path($uploadPath), 0755, true);
                            }

                            // Move the uploaded file to the specified path
                            if (!$file->move(public_path($uploadPath), $newFileName)) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Makalah gagal diupload.',
                                ], 500);
                            }

                            // Save the file details in the database
                            $params = [
                                'file_name_makalah' => $newFileName,
                                'file_path_makalah' => $uploadPath,
                            ];

                            ApiUploadFileModel::uploadFileMHS($request->id_mahasiswa, $params);

                            return response()->json([
                                'status' => true,
                                'message' => 'Data berhasil disimpan.',
                            ], 200);
                        }

                        return response()->json([
                            'status' => false,
                            'message' => 'Makalah tidak ditemukan.',
                        ], 400);
                    } else {
                        $response = [
                            'status' => false,
                            'message' => 'Token tidak valid!',
                            'data' => null,
                        ];
                        return response()->json($response, 401); // 401 Unauthorized
                    }
                }
            }
        } else {
            // User not found or api_token is null
            $response = [
                'status' => false,
                'message' => 'Pengguna tidak ditemukan!',
                'data' => null,
            ];
            return response()->json($response, 404); // 401 Unauthorized
        }
    }

    public function uploadLaporanProcess(Request $request)
    {
        // Get api_token from the request body
        $apiToken = $request->input('api_token');

        // Check if api_token is provided
        if (empty($apiToken)) {
            return response()->json([
                'status' => false,
                'message' => 'Missing api_token in the request body.',
                'data' => null,
            ], Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->input('user_id');
        $user = ApiAccountModel::getById($userId);

        // Check if the user exists
        if ($user != null) {
            // Attempt to authenticate the user based on api_token
            if ($user->api_token != null) {
                // Authorize
                $isAuthorized = ApiAccountModel::authorize('C', $userId);

                if (!$isAuthorized) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Akses tidak sah!',
                        'data' => null,
                    ], Response::HTTP_FORBIDDEN);
                } else {
                    // Check if the provided api_token matches the user's api_token
                    if ($user->api_token == $apiToken) {
                        // Validate the request
                        $validator = Validator::make($request->all(), [
                            'laporan_ta' => 'required|file|mimes:pdf|max:10240',
                            'id_mahasiswa' => 'required|exists:kelompok_mhs,id_mahasiswa',
                        ]);

                        // Check if validation fails
                        if ($validator->fails()) {
                            return response()->json([
                                'status' => false,
                                'message' => 'Validation error',
                                'errors' => $validator->errors(),
                            ], Response::HTTP_BAD_REQUEST);
                        }

                        // Upload path
                        $uploadPath = '/file/mahasiswa/laporan-ta';

                        // Upload Laporan TA
                        if ($request->hasFile('laporan_ta')) {
                            $file = $request->file('laporan_ta');

                            // Generate a unique file name
                            $newFileName = 'laporan_ta_' . Str::slug($user->user_name, '-') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                            // Check if the folder exists, if not, create it
                            if (!is_dir(public_path($uploadPath))) {
                                mkdir(public_path($uploadPath), 0755, true);
                            }

                            // Move the uploaded file to the specified path
                            try {
                                $file->move(public_path($uploadPath), $newFileName);
                            } catch (\Exception $e) {
                                return response()->json([
                                    'status' => false,
                                    'message' => 'Laporan gagal diupload.',
                                ], Response::HTTP_INTERNAL_SERVER_ERROR);
                            }

                            // Save the file details in the database
                            $params = [
                                'file_name_laporan_ta' => $newFileName,
                                'file_path_laporan_ta' => $uploadPath,
                            ];

                            ApiUploadFileModel::uploadFileMHS($request->id_mahasiswa, $params);

                            return response()->json([
                                'status' => true,
                                'message' => 'Data berhasil disimpan.',
                            ], Response::HTTP_OK);
                        }

                        return response()->json([
                            'status' => false,
                            'message' => 'Laporan tidak ditemukan.',
                        ], Response::HTTP_BAD_REQUEST);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Token tidak valid!',
                            'data' => null,
                        ], Response::HTTP_UNAUTHORIZED);
                    }
                }
            }
        } else {
            // User not found or api_token is null
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan!',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }
    }

}
