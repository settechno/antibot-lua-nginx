<?php

namespace App\Controller\Api;

use App\Antibot;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AntibotController extends ApiController {

    /**
     * @var Antibot
     */
    protected $antibot;

    public function __construct(Antibot $antibot) {
        $this->antibot = $antibot;
    }

    /**
     * @return JsonResponse
     * @Route("/antibot/list", name="Get ban list", methods={"GET"})
     */
    public function getBanList(): JsonResponse {
        $ips = $this->antibot->getBanList();
        return $this->json($ips);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/antibot/add", name="Add IP to ban", methods={"POST"})
     */
    public function add(Request $request): JsonResponse {
        try {
            $request = $this->transformJsonBody($request);
            $ip = $request->get('ip');
            $time = intval($request->request->get('time'));

            if (!$request || !$ip || !$time){
                throw new \Exception("Invalid data");
            }

            foreach (explode(',', $ip) as $item) {
                $item = trim($item);
                if ($item) {
                    $this->antibot->add($item, $time, $request->request->get('url'));
                }
            }
            $this->antibot->save();

            return $this->json([
              'success' => true,
              'message' => "IP $ip was banned successfully"
            ]);

        }catch (\Exception $e){
            $data = [
              'success' => false,
              'errors' => $e->getMessage()
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/antibot/delete", name="Delete IP from ban", methods={"POST"})
     */
    public function delete(Request $request): JsonResponse {
        try {
            $request = $this->transformJsonBody($request);
            $ip = $request->get('ip');

            if (!$request || !$ip ){
                throw new \Exception("Invalid data");
            }

            $this->antibot->delete($ip);
            $this->antibot->save();

            return $this->json([
              'success' => true,
              'message' => "IP $ip was deleted successfully",
            ]);

        }catch (\Exception $e){
            $data = [
              'success' => false,
              'errors' => $e->getMessage(),
            ];
            return $this->json($data, 422);
        }
    }

    /**
     *
     * @return JsonResponse
     * @Route("/antibot/clear", name="Clear ban list", methods={"GET"})
     */
    public function clear(): JsonResponse {
        $this->antibot->clearBanList();
        return $this->json([
          'success' => true,
          'message' => "Banlist was cleared successfully",
        ]);
    }
}