<?php

include_once 'JWT.php';
include_once 'JWK.php';
include_once 'BeforeValidException.php';
include_once 'ExpiredException.php';
include_once 'SignatureInvalidException.php';
include_once 'Key.php';


function generateJwtToken($data) {
    $now = time();

    $jwtToken = JWT\JWT::encode([
                "iat" => $now,
                "nbf" => $now,
                "exp" => $now + JWT_SESSION_TIME, // expiry + 1 HOUR from now
                "jti" => base64_encode(random_bytes(16)), // json token id
                "iss" => JWT_ISSUER, // issuer
                "aud" => JWT_AUD, // audience
                "data" => $data // whatever data you want to add
                    ], JWT_SECRET, JWT_ALGO);

    return $jwtToken;
}

function getStoredDataFromJwtToken($token) {
    try {
        return JWT\JWT::decode($token, new JWT\Key(JWT_SECRET, JWT_ALGO));
    } catch (JWT\ExpiredException $e) {
        return false;
    } catch (JWT\SignatureInvalidException $e) {
        return false;
    } catch (JWT\BeforeValidException $e) {
        return false;
    } catch (JWT\UnexpectedValueException $e) {
        return false;
    }
}
     
?>